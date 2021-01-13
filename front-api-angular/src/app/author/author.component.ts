import { UserService } from '../sevices/user.service';
import { Component, OnInit } from '@angular/core';

@Component({
  selector: 'app-author',
  templateUrl: './author.component.html',
  styleUrls: ['./author.component.css'],
})
export class AuthorComponent implements OnInit {
  constructor(private userService: UserService) {}
  users: any = [];
  ngOnInit(): void {
    this.getUserComponent();
  }

  getUserComponent() {
    this.userService.getAllUser().subscribe((data) => {
      this.users = data;
    });
  }
}
